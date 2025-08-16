

<?php $__env->startSection('title', 'Reservation Details'); ?>
<?php $__env->startSection('header', 'Laboratory Reservation Details'); ?>

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

    <!-- Action Buttons -->
    <div class="flex justify-end">
        <a href="<?php echo e(route('admin.laboratory.reservations.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 mr-2">
            Back to List
        </a>
        
        <?php if($reservation->status == 'pending'): ?>
            <form action="<?php echo e(route('admin.laboratory.reservations.approve', $reservation)); ?>" method="POST" class="inline mr-2">
                <?php echo csrf_field(); ?>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                    Approve
                </button>
            </form>
            
            <button onclick="openRejectModal()" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                Reject
            </button>
        <?php endif; ?>
          <?php if(in_array($reservation->status, ['rejected', 'cancelled'])): ?>
            <button type="button" 
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                    data-modal-target="deleteReservationModal">
                Delete
            </button>
        <?php endif; ?>
    </div>

    <!-- Reservation Details Card -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-medium text-gray-900">Reservation #<?php echo e($reservation->id); ?></h2>
            <?php if (isset($component)) { $__componentOriginal8860cf004fec956b6e41d036eb967550 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8860cf004fec956b6e41d036eb967550 = $attributes; } ?>
<?php $component = App\View\Components\StatusBadge::resolve(['status' => $reservation->status,'type' => 'reservation'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\StatusBadge::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'px-3 py-1 text-sm']); ?>
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
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Requested By</h3>
                    <p class="mt-1 text-base text-gray-900"><?php echo e($reservation->user->name); ?></p>
                    <p class="text-sm text-gray-600"><?php echo e($reservation->user->email); ?> (<?php echo e(ucfirst($reservation->user->role)); ?>)</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Submitted On</h3>
                    <p class="mt-1 text-base text-gray-900"><?php echo e($reservation->created_at->format('F d, Y - H:i')); ?></p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Laboratory</h3>
                    <p class="mt-1 text-base text-gray-900"><?php echo e($reservation->laboratory->name); ?></p>
                    <p class="text-sm text-gray-600"><?php echo e($reservation->laboratory->building); ?>, Room <?php echo e($reservation->laboratory->room_number); ?></p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Laboratory Capacity</h3>
                    <p class="mt-1 text-base text-gray-900"><?php echo e($reservation->laboratory->capacity); ?> seats / <?php echo e($reservation->laboratory->number_of_computers); ?> computers</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Date</h3>
                    <p class="mt-1 text-base text-gray-900"><?php echo e($reservation->reservation_date->format('F d, Y')); ?> (<?php echo e($reservation->reservation_date->format('l')); ?>)</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Time</h3>
                    <p class="mt-1 text-base text-gray-900">
                        <?php echo e(\Carbon\Carbon::parse($reservation->start_time)->format('H:i')); ?> - 
                        <?php echo e(\Carbon\Carbon::parse($reservation->end_time)->format('H:i')); ?>

                        <span class="text-sm text-gray-600">(<?php echo e($reservation->duration); ?>)</span>
                    </p>
                </div>

                <?php if($reservation->is_recurring): ?>
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500">Recurrence Pattern</h3>
                    <p class="mt-1 text-base text-gray-900">
                        <?php echo e(ucfirst($reservation->recurrence_pattern)); ?> until <?php echo e($reservation->recurrence_end_date->format('F d, Y')); ?>

                    </p>
                </div>
                <?php endif; ?>

                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500">Purpose</h3>
                    <p class="mt-1 text-base text-gray-900"><?php echo e($reservation->purpose); ?></p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Number of Students</h3>
                    <p class="mt-1 text-base text-gray-900"><?php echo e($reservation->num_students); ?></p>
                </div>

                <?php if($reservation->course_code || $reservation->subject || $reservation->section): ?>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Course Information</h3>
                    <p class="mt-1 text-base text-gray-900">
                        <?php if($reservation->course_code): ?>
                            <?php echo e($reservation->course_code); ?>:
                        <?php endif; ?>
                        <?php echo e($reservation->subject ?? 'N/A'); ?>

                        <?php if($reservation->section): ?>
                            (<?php echo e($reservation->section); ?>)
                        <?php endif; ?>
                    </p>
                </div>
                <?php endif; ?>

                <?php if($reservation->status === 'rejected' && $reservation->rejection_reason): ?>
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-sm font-medium text-red-500">Rejection Reason</h3>
                    <p class="mt-1 text-base text-red-600"><?php echo e($reservation->rejection_reason); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Potential Conflicts Section -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Potential Conflicts</h2>
        </div>
        <div class="p-6">
            <h3 class="text-base font-medium text-gray-800 mb-3">Regular Class Schedule Conflicts</h3>
            
            <?php
                $dayOfWeek = \Carbon\Carbon::parse($reservation->reservation_date)->dayOfWeek;
                $conflictingSchedules = \App\Models\LaboratorySchedule::where('laboratory_id', $reservation->laboratory_id)
                    ->where('day_of_week', $dayOfWeek)
                    ->where(function($query) use ($reservation) {
                        $query->where(function($q) use ($reservation) {
                            $q->where('start_time', '<=', $reservation->start_time)
                              ->where('end_time', '>', $reservation->start_time);
                        })->orWhere(function($q) use ($reservation) {
                            $q->where('start_time', '<', $reservation->end_time)
                              ->where('end_time', '>=', $reservation->end_time);
                        })->orWhere(function($q) use ($reservation) {
                            $q->where('start_time', '>=', $reservation->start_time)
                              ->where('end_time', '<=', $reservation->end_time);
                        });
                    })
                    ->with('academicTerm')
                    ->get();
            ?>
            
            <?php if($conflictingSchedules->count() > 0): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                    <p class="font-bold">Warning: Schedule Conflicts Detected</p>
                    <p>This reservation conflicts with <?php echo e($conflictingSchedules->count()); ?> regular class schedule(s)!</p>
                </div>
                
                <div class="overflow-x-auto mt-3">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instructor</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Term</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $conflictingSchedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="bg-red-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php
                                        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                    ?>
                                    <?php echo e($days[$schedule->day_of_week]); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e(\Carbon\Carbon::parse($schedule->start_time)->format('H:i')); ?> - 
                                    <?php echo e(\Carbon\Carbon::parse($schedule->end_time)->format('H:i')); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo e($schedule->subject_code); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo e($schedule->subject_name); ?> (<?php echo e($schedule->section); ?>)</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e($schedule->instructor_name); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e($schedule->academicTerm->name); ?>

                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-green-600">No conflicts with regular class schedules.</p>
            <?php endif; ?>
            
            <h3 class="text-base font-medium text-gray-800 mt-6 mb-3">Other Reservation Conflicts</h3>
            
            <?php
                $conflictingReservations = \App\Models\LaboratoryReservation::where('laboratory_id', $reservation->laboratory_id)
                    ->where('reservation_date', $reservation->reservation_date)
                    ->where('status', \App\Models\LaboratoryReservation::STATUS_APPROVED)
                    ->where('id', '!=', $reservation->id)
                    ->where(function($query) use ($reservation) {
                        $query->where(function($q) use ($reservation) {
                            $q->where('start_time', '<=', $reservation->start_time)
                              ->where('end_time', '>', $reservation->start_time);
                        })->orWhere(function($q) use ($reservation) {
                            $q->where('start_time', '<', $reservation->end_time)
                              ->where('end_time', '>=', $reservation->end_time);
                        })->orWhere(function($q) use ($reservation) {
                            $q->where('start_time', '>=', $reservation->start_time)
                              ->where('end_time', '<=', $reservation->end_time);
                        });
                    })
                    ->with('user')
                    ->get();
            ?>
            
            <?php if($conflictingReservations->count() > 0): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                    <p class="font-bold">Warning: Reservation Conflicts Detected</p>
                    <p>This reservation conflicts with <?php echo e($conflictingReservations->count()); ?> existing approved reservation(s)!</p>
                </div>
                
                <div class="overflow-x-auto mt-3">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $conflictingReservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conflictReservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="bg-red-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo e($conflictReservation->id); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e(\Carbon\Carbon::parse($conflictReservation->start_time)->format('H:i')); ?> - 
                                    <?php echo e(\Carbon\Carbon::parse($conflictReservation->end_time)->format('H:i')); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo e($conflictReservation->user->name); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo e($conflictReservation->user->email); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e(\Illuminate\Support\Str::limit($conflictReservation->purpose, 30)); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="<?php echo e(route('admin.laboratory.reservations.show', $conflictReservation)); ?>" class="text-blue-600 hover:text-blue-900">View</a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-green-600">No conflicts with other approved reservations.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<?php if (isset($component)) { $__componentOriginalb1a293b1d35a361cb551af6e0ce13e13 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb1a293b1d35a361cb551af6e0ce13e13 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rejection-modal','data' => ['actionUrl' => route('admin.laboratory.reservations.reject', $reservation)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rejection-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.laboratory.reservations.reject', $reservation))]); ?>
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

<!-- Delete Confirmation Modal -->
<?php if(in_array($reservation->status, ['rejected', 'cancelled'])): ?>
<?php if (isset($component)) { $__componentOriginal8b7b112f0fae85419ee5abf8337434ab = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8b7b112f0fae85419ee5abf8337434ab = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delete-confirmation-modal','data' => ['modalId' => 'deleteReservationModal','title' => 'Delete Reservation','message' => 'Are you sure you want to delete this reservation? This action cannot be undone.','deleteRoute' => ''.e(route('admin.laboratory.reservations.destroy', $reservation)).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delete-confirmation-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['modal-id' => 'deleteReservationModal','title' => 'Delete Reservation','message' => 'Are you sure you want to delete this reservation? This action cannot be undone.','delete-route' => ''.e(route('admin.laboratory.reservations.destroy', $reservation)).'']); ?>
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

<?php $__env->startPush('scripts'); ?>
<!-- Reservation rejection functionality is now handled by reservation-manager.js module -->
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views\admin\laboratory\reservations\show.blade.php ENDPATH**/ ?>