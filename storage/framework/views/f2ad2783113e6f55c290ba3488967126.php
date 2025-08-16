

<?php $__env->startSection('title', 'User Details'); ?>
<?php $__env->startSection('header', 'User Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if (isset($component)) { $__componentOriginalcca61bfded94b5a7635453a4dc55dd1d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcca61bfded94b5a7635453a4dc55dd1d = $attributes; } ?>
<?php $component = App\View\Components\FlashMessages::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flash-messages'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\FlashMessages::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcca61bfded94b5a7635453a4dc55dd1d)): ?>
<?php $attributes = $__attributesOriginalcca61bfded94b5a7635453a4dc55dd1d; ?>
<?php unset($__attributesOriginalcca61bfded94b5a7635453a4dc55dd1d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcca61bfded94b5a7635453a4dc55dd1d)): ?>
<?php $component = $__componentOriginalcca61bfded94b5a7635453a4dc55dd1d; ?>
<?php unset($__componentOriginalcca61bfded94b5a7635453a4dc55dd1d); ?>
<?php endif; ?>

    <!-- Header with actions -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800"><?php echo e($user->name); ?></h1>
        <div class="flex space-x-3">
            <a href="<?php echo e(route('admin.users.index')); ?>" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i> Back to Users
            </a>
            <a href="<?php echo e(route('admin.users.edit', $user)); ?>" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                <i class="fas fa-edit mr-2"></i> Edit User
            </a>
            <a href="<?php echo e(route('admin.users.reset-password', $user)); ?>" 
               class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700">
                <i class="fas fa-key mr-2"></i> Reset Password
            </a>
        </div>
    </div>

    <!-- User Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Profile -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="text-center">
                    <div class="mx-auto h-32 w-32 rounded-full bg-gray-300 flex items-center justify-center mb-4">
                        <i class="fas fa-user text-gray-500 text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900"><?php echo e($user->name); ?></h3>
                    <p class="text-sm text-gray-500 mb-4"><?php echo e($user->email); ?></p>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Role:</span>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                <?php echo e($user->role === 'student' ? 'bg-blue-100 text-blue-800' : ''); ?>

                                <?php echo e($user->role === 'faculty' ? 'bg-green-100 text-green-800' : ''); ?>

                                <?php echo e($user->role === 'staff' ? 'bg-purple-100 text-purple-800' : ''); ?>">
                                <?php echo e(ucfirst($user->role)); ?>

                            </span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Department:</span>
                            <span class="text-sm text-gray-900"><?php echo e($user->department ?? 'N/A'); ?></span>
                        </div>
                        
                        <?php if($user->contact_number): ?>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Contact:</span>
                            <span class="text-sm text-gray-900"><?php echo e($user->contact_number); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">RFID Tag:</span>
                            <?php if($user->rfid_tag): ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-id-card mr-1"></i>
                                    <?php echo e($user->rfid_tag); ?>

                                </span>
                            <?php else: ?>
                                <span class="text-sm text-gray-400">Not set</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Member Since:</span>
                            <span class="text-sm text-gray-900"><?php echo e($user->created_at->format('M d, Y')); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics and Activity -->
        <div class="lg:col-span-2">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-box text-blue-500 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-500">Equipment Requests</div>
                            <div class="text-2xl font-bold text-gray-900"><?php echo e($stats['total_equipment_requests']); ?></div>
                            <div class="text-xs text-green-600"><?php echo e($stats['approved_equipment_requests']); ?> approved</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-hand-holding text-yellow-500 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-500">Currently Borrowed</div>
                            <div class="text-2xl font-bold text-gray-900"><?php echo e($stats['currently_borrowed']); ?></div>
                            <div class="text-xs text-gray-500">equipment items</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <?php if($user->rfid_tag): ?>
                                <i class="fas fa-id-card text-green-500 text-2xl"></i>
                            <?php else: ?>
                                <i class="fas fa-id-card text-gray-400 text-2xl"></i>
                            <?php endif; ?>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-500">RFID Status</div>
                            <div class="text-lg font-bold <?php echo e($user->rfid_tag ? 'text-green-600' : 'text-gray-400'); ?>">
                                <?php echo e($user->rfid_tag ? 'Configured' : 'Not Set'); ?>

                            </div>
                            <div class="text-xs text-gray-500"><?php echo e($user->rfid_tag ? 'Ready for onsite borrowing' : 'Manual entry required'); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Equipment Requests -->
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Equipment Requests</h3>
                </div>
                <div class="p-6">
                    <?php if($user->equipmentRequests->count() > 0): ?>
                        <div class="space-y-4">
                            <?php $__currentLoopData = $user->equipmentRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-box text-gray-400"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?php echo e($request->equipment->name ?? 'Equipment N/A'); ?></div>
                                            <div class="text-xs text-gray-500"><?php echo e($request->created_at->format('M d, Y H:i')); ?></div>
                                            <?php if($request->purpose): ?>
                                                <div class="text-xs text-gray-600 mt-1"><?php echo e(Str::limit($request->purpose, 50)); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            <?php echo e($request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ''); ?>

                                            <?php echo e($request->status === 'approved' ? 'bg-green-100 text-green-800' : ''); ?>

                                            <?php echo e($request->status === 'rejected' ? 'bg-red-100 text-red-800' : ''); ?>">
                                            <?php echo e(ucfirst($request->status)); ?>

                                        </span>
                                        <?php if($request->status === 'approved' && $request->returned_at): ?>
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Returned
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        
                        <?php if($user->equipmentRequests()->count() > 10): ?>
                            <div class="mt-4 text-center">
                                <p class="text-sm text-gray-500">Showing latest 10 requests</p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-4">No equipment requests found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views\admin\users\show.blade.php ENDPATH**/ ?>