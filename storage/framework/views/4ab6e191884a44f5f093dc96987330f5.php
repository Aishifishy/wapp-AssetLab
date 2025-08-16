  

<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page-title', 'Dashboard Overview'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <!-- Equipment Overview -->
    <div class="col-12 col-lg-6 mb-4">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="d-flex justify-content-between align-items-center mb-6">
                <h3 class="text-lg font-semibold">Equipment Status</h3>
                <i class="fas fa-tools text-blue-500 text-2xl"></i>
            </div>
            <div class="row g-2">
                <div class="col-12 col-sm-4">
                    <div class="p-3 bg-blue-50 rounded-lg text-center">
                        <div class="text-gray-600 text-sm">Total Equipment</div>
                        <div class="font-bold text-blue-600 text-xl mt-2"><?php echo e($totalEquipment ?? 0); ?></div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="p-3 bg-yellow-50 rounded-lg text-center">
                        <div class="text-gray-600 text-sm">Currently Borrowed</div>
                        <div class="font-bold text-yellow-600 text-xl mt-2"><?php echo e($borrowedEquipment ?? 0); ?></div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="p-3 bg-red-50 rounded-lg text-center">
                        <div class="text-gray-600 text-sm">Pending Requests</div>
                        <div class="font-bold text-red-600 text-xl mt-2"><?php echo e($pendingRequests ?? 0); ?></div>
                    </div>
                </div>
            </div>
            <a href="<?php echo e(route('admin.equipment.manage')); ?>" class="mt-4 d-inline-flex align-items-center text-blue-600 hover:text-blue-800 text-sm">
                Manage Equipment <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    <!-- Laboratory Overview -->
    <div class="col-12 col-lg-6 mb-4">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="d-flex justify-content-between align-items-center mb-6">
                <h3 class="text-lg font-semibold">Laboratory Status</h3>
                <i class="fas fa-desktop text-green-500 text-2xl"></i>
            </div>
                        <div class="row g-2">
                <div class="col-12 col-sm-4">
                    <div class="p-3 bg-green-50 rounded-lg text-center">
                        <div class="text-gray-600 text-sm">Today's Bookings</div>
                        <div class="font-bold text-green-600 text-xl mt-2"><?php echo e($todaysBookings ?? 0); ?></div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="p-3 bg-orange-50 rounded-lg text-center">
                        <div class="text-gray-600 text-sm">Pending Reservation</div>
                        <div class="font-bold text-orange-600 text-xl mt-2"><?php echo e($pendingReservations ?? 0); ?></div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="p-3 bg-purple-50 rounded-lg text-center">
                        <div class="text-gray-600 text-sm">Active Classes</div>
                        <div class="font-bold text-purple-600 text-xl mt-2"><?php echo e($activeClasses ?? 0); ?></div>
                    </div>
                </div>
            </div>
            <a href="<?php echo e(route('admin.comlab.calendar')); ?>" class="mt-4 d-inline-flex align-items-center text-green-600 hover:text-green-800 text-sm">
                View Calendar <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="mt-8">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="text-xl font-semibold">Recent Activities</h3>
                
                <!-- Activity Filter Buttons -->
                <div class="inline-flex rounded-md shadow-sm">
                    <a href="<?php echo e(route('admin.dashboard', ['activity_type' => 'all'])); ?>" 
                       class="px-4 py-2 text-sm font-medium <?php echo e($activityType == 'all' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'); ?> border border-gray-300 rounded-l-md">
                        All
                    </a>
                    <a href="<?php echo e(route('admin.dashboard', ['activity_type' => 'equipment'])); ?>" 
                       class="px-4 py-2 text-sm font-medium <?php echo e($activityType == 'equipment' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'); ?> border-t border-b border-r border-gray-300">
                        Equipment
                    </a>
                    <a href="<?php echo e(route('admin.dashboard', ['activity_type' => 'laboratory'])); ?>" 
                       class="px-4 py-2 text-sm font-medium <?php echo e($activityType == 'laboratory' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'); ?> border-t border-b border-r border-gray-300 rounded-r-md">
                        Laboratory
                    </a>
                </div>
            </div>
        </div>
        
        <div class="p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?php echo e($activityType == 'laboratory' ? 'Laboratory' : 'Equipment'); ?>

                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $recentActivities ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo e($activity->created_at ? $activity->created_at->diffForHumans() : 'N/A'); ?>

                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900">
                                <?php echo e($activity->description ?? 'N/A'); ?>

                                <?php if(isset($activity->notes)): ?>
                                <p class="text-xs text-gray-500 mt-1"><?php echo e(Str::limit($activity->notes, 50)); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php echo e($activity->user_name ?? 'N/A'); ?>

                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                                <?php echo e($activity->item_name ?? 'N/A'); ?>

                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <?php if (isset($component)) { $__componentOriginal8860cf004fec956b6e41d036eb967550 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8860cf004fec956b6e41d036eb967550 = $attributes; } ?>
<?php $component = App\View\Components\StatusBadge::resolve(['status' => $activity->status ?? 'unknown','type' => $activity->activity_type ?? 'equipment'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\StatusBadge::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8860cf004fec956b6e41d036eb967550)): ?>
<?php $attributes = $__attributesOriginal8860cf004fec956b6e41d036eb967550; ?>
<?php unset($__attributesOriginal8860cf004fec956b6e41d036eb967550); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8860cf004fec956b6e41d036eb967550)): ?>
<?php $component = $__componentOriginal8860cf004fec956b6e41d036eb967550; ?>
<?php unset($__componentOriginal8860cf004fec956b6e41d036eb967550); ?>
<?php endif; ?>
                            </td>
                            <td class="px-4 py-4 text-sm whitespace-nowrap">
                                <a href="#" class="text-blue-600 hover:text-blue-900">View Details</a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                                <?php if($activityType == 'laboratory'): ?>
                                    No laboratory activities found
                                <?php elseif($activityType == 'equipment'): ?>
                                    No equipment activities found
                                <?php else: ?>
                                    No recent activities found
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 flex justify-end">
                <a href="#" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                    View All Activities 
                    <svg class="ml-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<!-- <?php $__env->startPush('scripts'); ?>
<script>
    // Add any dashboard-specific JavaScript here
</script>
<?php $__env->stopPush(); ?>  -->
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views\admin\dashboard.blade.php ENDPATH**/ ?>