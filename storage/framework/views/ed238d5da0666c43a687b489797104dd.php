

<?php $__env->startSection('title', 'My Laboratory Reservations'); ?>
<?php $__env->startSection('header', 'My Laboratory Reservations'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row flex-wrap gap-3 sm:gap-4">
        <a href="<?php echo e(route('ruser.laboratory.reservations.calendar')); ?>" 
           class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded flex items-center justify-center sm:justify-start">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Calendar View
        </a>
        
        <a href="<?php echo e(route('ruser.laboratory.reservations.quick')); ?>" 
           class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center justify-center sm:justify-start">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <span class="truncate">Quick Reserve</span>
        </a>
        
        <a href="<?php echo e(route('ruser.laboratory.index')); ?>" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center justify-center sm:justify-start sm:ml-auto">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <span class="truncate">Browse Laboratories</span>
            Make New Reservation
        </a>
    </div>

    <!-- Pending Reservations Section -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Pending Reservations</h2>
        </div>
        <div class="p-6">
            <?php if($pendingReservations->count() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laboratory</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $pendingReservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo e($reservation->laboratory->name); ?>

                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <?php echo e($reservation->laboratory->building); ?>, Room <?php echo e($reservation->laboratory->room_number); ?>

                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?php echo e($reservation->reservation_date->format('M d, Y')); ?>

                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?php echo e(\Carbon\Carbon::parse($reservation->start_time)->format('H:i')); ?> - 
                                            <?php echo e(\Carbon\Carbon::parse($reservation->end_time)->format('H:i')); ?>

                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Duration: <?php echo e($reservation->duration); ?>

                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 truncate max-w-xs">
                                            <?php echo e(\Illuminate\Support\Str::limit($reservation->purpose, 50)); ?>

                                        </div>
                                    </td>                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if (isset($component)) { $__componentOriginal8860cf004fec956b6e41d036eb967550 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8860cf004fec956b6e41d036eb967550 = $attributes; } ?>
<?php $component = App\View\Components\StatusBadge::resolve(['status' => 'pending','type' => 'reservation'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                                    </td>                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex flex-col sm:flex-row sm:justify-end space-y-1 sm:space-y-0 sm:space-x-4">
                                            <a href="<?php echo e(route('ruser.laboratory.reservations.show', $reservation)); ?>" class="text-blue-600 hover:text-blue-900 text-center sm:text-left">View</a>
                                            <form action="<?php echo e(route('ruser.laboratory.reservations.cancel', $reservation)); ?>" method="POST" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="text-red-600 hover:text-red-900 confirm-action w-full sm:w-auto" data-confirm-message="Are you sure you want to cancel this reservation?">Cancel</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500">You don't have any pending laboratory reservations.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Upcoming Reservations Section -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Upcoming Reservations</h2>
        </div>
        <div class="p-6">
            <?php if($upcomingReservations->count() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laboratory</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $upcomingReservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo e($reservation->laboratory->name); ?>

                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <?php echo e($reservation->laboratory->building); ?>, Room <?php echo e($reservation->laboratory->room_number); ?>

                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?php echo e($reservation->reservation_date->format('M d, Y')); ?>

                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?php echo e(\Carbon\Carbon::parse($reservation->start_time)->format('H:i')); ?> - 
                                            <?php echo e(\Carbon\Carbon::parse($reservation->end_time)->format('H:i')); ?>

                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Duration: <?php echo e($reservation->duration); ?>

                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 truncate max-w-xs">
                                            <?php echo e(\Illuminate\Support\Str::limit($reservation->purpose, 50)); ?>

                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex flex-col sm:flex-row sm:justify-end space-y-1 sm:space-y-0 sm:space-x-4">
                                            <a href="<?php echo e(route('ruser.laboratory.reservations.show', $reservation)); ?>" class="text-blue-600 hover:text-blue-900 text-center sm:text-left">View</a>
                                            <?php
                                                $canCancel = \Carbon\Carbon::parse($reservation->reservation_date . ' ' . $reservation->start_time)->diffInHours(now()) >= 24;
                                            ?>
                                              <?php if($canCancel): ?>
                                                <form action="<?php echo e(route('ruser.laboratory.reservations.cancel', $reservation)); ?>" method="POST" class="inline">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="text-red-600 hover:text-red-900 confirm-action w-full sm:w-auto" data-confirm-message="Are you sure you want to cancel this reservation?">Cancel</button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-gray-400 text-center sm:text-left" title="Reservations cannot be cancelled within 24 hours of start time">Cannot Cancel</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500">You don't have any upcoming laboratory reservations.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Past/Rejected Reservations Section -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Past/Cancelled/Rejected Reservations</h2>
        </div>
        <div class="p-6">
            <?php if($pastReservations->count() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laboratory</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $pastReservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo e($reservation->laboratory->name); ?>

                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <?php echo e($reservation->laboratory->building); ?>, Room <?php echo e($reservation->laboratory->room_number); ?>

                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?php echo e($reservation->reservation_date->format('M d, Y')); ?>

                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?php echo e(\Carbon\Carbon::parse($reservation->start_time)->format('H:i')); ?> - 
                                            <?php echo e(\Carbon\Carbon::parse($reservation->end_time)->format('H:i')); ?>

                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php if($reservation->status == 'rejected'): ?>
                                            bg-red-100 text-red-800
                                        <?php elseif($reservation->status == 'cancelled'): ?>
                                            bg-gray-100 text-gray-800
                                        <?php else: ?>
                                            bg-blue-100 text-blue-800
                                        <?php endif; ?>">
                                            <?php echo e(ucfirst($reservation->status)); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-center sm:justify-end">
                                            <a href="<?php echo e(route('ruser.laboratory.reservations.show', $reservation)); ?>" class="text-blue-600 hover:text-blue-900">View</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    <?php echo e($pastReservations->links()); ?>

                </div>
            <?php else: ?>
                <p class="text-gray-500">You don't have any past laboratory reservations.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('l            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 002 2z" />
            </svg>
            <span class="truncate">Calendar View</span>ts.ruser', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views\ruser\laboratory\reservation\index.blade.php ENDPATH**/ ?>