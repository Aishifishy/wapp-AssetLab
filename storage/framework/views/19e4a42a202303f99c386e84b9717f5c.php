

<?php $__env->startSection('title', 'Laboratory Reservations'); ?>
<?php $__env->startSection('header', 'Laboratory Reservations'); ?>

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

    <!-- Filters Section -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Filters</h2>
        </div>
        <div class="p-6">
            <form action="<?php echo e(route('admin.laboratory.reservations.index')); ?>" method="GET">
                <div class="flex flex-wrap gap-4">
                    <div class="flex-grow min-w-[200px]">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="status" name="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="all" <?php echo e($status == 'all' ? 'selected' : ''); ?>>All (<?php echo e($statusCounts['all']); ?>)</option>
                            <option value="pending" <?php echo e($status == 'pending' ? 'selected' : ''); ?>>Pending (<?php echo e($statusCounts['pending']); ?>)</option>
                            <option value="approved" <?php echo e($status == 'approved' ? 'selected' : ''); ?>>Approved (<?php echo e($statusCounts['approved']); ?>)</option>
                            <option value="rejected" <?php echo e($status == 'rejected' ? 'selected' : ''); ?>>Rejected (<?php echo e($statusCounts['rejected']); ?>)</option>
                            <option value="cancelled" <?php echo e($status == 'cancelled' ? 'selected' : ''); ?>>Cancelled (<?php echo e($statusCounts['cancelled']); ?>)</option>
                        </select>
                    </div>
                    <div class="flex-grow min-w-[200px]">
                        <label for="laboratory" class="block text-sm font-medium text-gray-700 mb-1">Laboratory</label>
                        <select id="laboratory" name="laboratory" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">All Laboratories</option>
                            <?php $__currentLoopData = $laboratories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($lab->id); ?>" <?php echo e($laboratory == $lab->id ? 'selected' : ''); ?>>
                                    <?php echo e($lab->name); ?> (<?php echo e($lab->building); ?>, Room <?php echo e($lab->room_number); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Reservations List -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">
                Laboratory Reservations 
                <?php if($status !== 'all'): ?>
                    - <?php echo e(ucfirst($status)); ?>

                <?php endif; ?>
            </h2>
        </div>
        <div class="p-6">
            <?php if($reservations->count() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laboratory</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $reservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-900"><?php echo e($reservation->id); ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($reservation->user->name); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo e($reservation->user->email); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($reservation->laboratory->name); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo e($reservation->laboratory->building); ?>, Room <?php echo e($reservation->laboratory->room_number); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo e($reservation->reservation_date->format('M d, Y')); ?></div>
                                        <div class="text-sm text-gray-500">
                                            <?php echo e(\Carbon\Carbon::parse($reservation->start_time)->format('H:i')); ?> - 
                                            <?php echo e(\Carbon\Carbon::parse($reservation->end_time)->format('H:i')); ?>

                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">                                        <?php if (isset($component)) { $__componentOriginal8860cf004fec956b6e41d036eb967550 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8860cf004fec956b6e41d036eb967550 = $attributes; } ?>
<?php $component = App\View\Components\StatusBadge::resolve(['status' => $reservation->status,'type' => 'reservation'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo e($reservation->created_at->format('M d, Y H:i')); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="<?php echo e(route('admin.laboratory.reservations.show', $reservation)); ?>" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                        
                                        <?php if($reservation->status == 'pending'): ?>
                                            <a href="#" onclick="document.getElementById('approve-form-<?php echo e($reservation->id); ?>').submit();" class="text-green-600 hover:text-green-900 mr-3">Approve</a>
                                            <form id="approve-form-<?php echo e($reservation->id); ?>" action="<?php echo e(route('admin.laboratory.reservations.approve', $reservation)); ?>" method="POST" class="hidden">
                                                <?php echo csrf_field(); ?>
                                            </form>
                                            
                                            <a href="#" onclick="openRejectModal(<?php echo e($reservation->id); ?>)" class="text-red-600 hover:text-red-900">Reject</a>
                                        <?php endif; ?>
                                          <?php if(in_array($reservation->status, ['rejected', 'cancelled'])): ?>
                                            <button type="button" 
                                                    class="text-gray-600 hover:text-gray-900"
                                                    data-modal-target="deleteModal<?php echo e($reservation->id); ?>">
                                                Delete
                                            </button>
                                        <?php endif; ?>
                                    </td>                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Delete Confirmation Modals for each deletable reservation -->
                <?php $__currentLoopData = $reservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(in_array($reservation->status, ['rejected', 'cancelled'])): ?>
                        <?php if (isset($component)) { $__componentOriginal8b7b112f0fae85419ee5abf8337434ab = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8b7b112f0fae85419ee5abf8337434ab = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delete-confirmation-modal','data' => ['modalId' => 'deleteModal'.e($reservation->id).'','title' => 'Delete Reservation','message' => 'Are you sure you want to delete this reservation? This action cannot be undone.','itemName' => 'Reservation #'.e($reservation->id).'','deleteRoute' => ''.e(route('admin.laboratory.reservations.destroy', $reservation)).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delete-confirmation-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['modal-id' => 'deleteModal'.e($reservation->id).'','title' => 'Delete Reservation','message' => 'Are you sure you want to delete this reservation? This action cannot be undone.','item-name' => 'Reservation #'.e($reservation->id).'','delete-route' => ''.e(route('admin.laboratory.reservations.destroy', $reservation)).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8b7b112f0fae85419ee5abf8337434ab)): ?>
<?php $attributes = $__attributesOriginal8b7b112f0fae85419ee5abf8337434ab; ?>
<?php unset($__attributesOriginal8b7b112f0fae85419ee5abf8337434ab); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8b7b112f0fae85419ee5abf8337434ab)): ?>
<?php $component = $__componentOriginal8b7b112f0fae85419ee5abf8337434ab; ?>
<?php unset($__componentOriginal8b7b112f0fae85419ee5abf8337434ab); ?>
<?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
                <div class="mt-4">
                    <?php echo e($reservations->appends(request()->query())->links()); ?>

                </div>
            <?php else: ?>
                <p class="text-gray-500">No reservations found matching the current filters.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<?php if (isset($component)) { $__componentOriginalb1a293b1d35a361cb551af6e0ce13e13 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb1a293b1d35a361cb551af6e0ce13e13 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rejection-modal','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rejection-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb1a293b1d35a361cb551af6e0ce13e13)): ?>
<?php $attributes = $__attributesOriginalb1a293b1d35a361cb551af6e0ce13e13; ?>
<?php unset($__attributesOriginalb1a293b1d35a361cb551af6e0ce13e13); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb1a293b1d35a361cb551af6e0ce13e13)): ?>
<?php $component = $__componentOriginalb1a293b1d35a361cb551af6e0ce13e13; ?>
<?php unset($__componentOriginalb1a293b1d35a361cb551af6e0ce13e13); ?>
<?php endif; ?>

<?php $__env->startPush('scripts'); ?>
<!-- Reservation rejection functionality is now handled by reservation-manager.js module -->
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views\admin\laboratory\reservations\index.blade.php ENDPATH**/ ?>