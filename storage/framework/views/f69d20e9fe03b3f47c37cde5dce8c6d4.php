

<?php $__env->startSection('title', 'Currently Borrowed Equipment'); ?>
<?php $__env->startSection('header', 'Currently Borrowed Equipment'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
    <!-- Header with action button -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="px-4 py-3 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
                <h2 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2" />
                    </svg>
                    My Borrowed Equipment
                </h2>
                <a href="<?php echo e(route('ruser.equipment.borrow')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Borrow More Equipment
                </a>
            </div>
        </div>
    </div>

    <?php if($borrowedRequests->count() > 0): ?>
        <!-- Equipment Cards -->
        <div class="space-y-4">
            <?php $__currentLoopData = $borrowedRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-4">
                        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start space-y-4 lg:space-y-0">
                            <div class="flex-1">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900"><?php echo e($request->equipment->name); ?></h3>
                                        <?php if($request->equipment->description): ?>
                                            <p class="text-gray-600 mt-1"><?php echo e($request->equipment->description); ?></p>
                                        <?php endif; ?>
                                        
                                        <!-- Equipment Details Grid -->
                                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                            <div>
                                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Borrowed From</dt>
                                                <dd class="mt-1">
                                                    <div class="text-sm font-medium text-gray-900"><?php echo e($request->requested_from->format('M d, Y')); ?></div>
                                                    <div class="text-xs text-gray-500"><?php echo e($request->requested_from->format('g:i A')); ?></div>
                                                </dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Return By</dt>
                                                <dd class="mt-1">
                                                    <div class="text-sm font-medium text-gray-900"><?php echo e($request->requested_until->format('M d, Y')); ?></div>
                                                    <div class="text-xs text-gray-500"><?php echo e($request->requested_until->format('g:i A')); ?></div>
                                                    <?php if($request->requested_until < now()): ?>
                                                        <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">
                                                            ⚠️ OVERDUE
                                                        </div>
                                                    <?php endif; ?>
                                                </dd>
                                            </div>
                                            <div class="sm:col-span-2 lg:col-span-1">
                                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status</dt>
                                                <dd class="mt-1">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <?php echo e(ucfirst($request->status)); ?>

                                                    </span>
                                                </dd>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</dt>
                                            <dd class="mt-1">
                                                <p class="text-sm text-gray-900"><?php echo e($request->purpose); ?></p>
                                            </dd>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Button -->
                            <div class="lg:ml-6">
                                <?php if(!$request->return_requested_at): ?>
                                    <form action="<?php echo e(route('ruser.equipment.return', $request)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" 
                                                class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition"
                                                onclick="return confirm('Are you sure you want to request return of this equipment?')">
                                            <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                                            </svg>
                                            Request Return
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="h-3 w-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Return Requested
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Pagination -->
        <?php if($borrowedRequests->hasPages()): ?>
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-4">
                    <?php echo e($borrowedRequests->links()); ?>

                </div>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <!-- Empty State -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-8 text-center">
                <div class="mx-auto h-24 w-24 text-gray-300 mb-4">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Equipment Currently Borrowed</h3>
                <p class="text-gray-600 mb-6">You don't have any equipment currently borrowed.</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="<?php echo e(route('ruser.equipment.borrow')); ?>" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Browse Equipment
                    </a>
                    <a href="<?php echo e(route('ruser.equipment.history')); ?>" 
                       class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        View History
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.ruser', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views/ruser/equipment/borrowed.blade.php ENDPATH**/ ?>